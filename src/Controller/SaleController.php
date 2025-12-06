<?php

namespace App\Controller;

use App\Entity\Sale;
use App\Entity\SaleItem;
use App\Form\SaleType;
use App\Repository\SaleRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sale')]
class SaleController extends AbstractController
{
    #[Route('/', name: 'app_sale_index')]
    public function index(
        Request $request,
        SaleRepository $saleRepository,
        PaginatorInterface $paginator
    ): Response {
        $search = $request->query->get('search', '');
        $startDate = $request->query->get('start_date', '');
        $endDate = $request->query->get('end_date', '');

        $queryBuilder = $saleRepository->createQueryBuilder('s')
            ->orderBy('s.saleDate', 'DESC');

        if ($search) {
            $queryBuilder
                ->leftJoin('s.customer', 'c')
                ->andWhere('s.invoiceNumber LIKE :search OR c.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($startDate) {
            $queryBuilder
                ->andWhere('s.saleDate >= :startDate')
                ->setParameter('startDate', new \DateTime($startDate));
        }

        if ($endDate) {
            $queryBuilder
                ->andWhere('s.saleDate <= :endDate')
                ->setParameter('endDate', new \DateTime($endDate . ' 23:59:59'));
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('sale/index.html.twig', [
            'page_title' => 'Historique des Ventes',
            'pagination' => $pagination,
            'search' => $search,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    #[Route('/new', name: 'app_sale_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $sale = new Sale();
        $sale->setSaleDate(new \DateTime());
        
        $form = $this->createForm(SaleType::class, $sale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Calculate total
            $total = 0;
            foreach ($sale->getSaleItems() as $item) {
                $item->setSale($sale);
                $total += $item->getQuantity() * $item->getUnitPrice();
            }
            $sale->setTotalAmount($total);

            $em->persist($sale);
            $em->flush();

            $this->addFlash('success', 'Vente enregistrée avec succès !');
            return $this->redirectToRoute('app_sale_show', ['id' => $sale->getId()]);
        }

        return $this->render('sale/new.html.twig', [
            'page_title' => 'Nouvelle Vente',
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sale_show', requirements: ['id' => '\d+'])]
    public function show(Sale $sale): Response
    {
        return $this->render('sale/show.html.twig', [
            'page_title' => 'Détails de la Vente',
            'sale' => $sale,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sale_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Sale $sale, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SaleType::class, $sale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Recalculate total
            $total = 0;
            foreach ($sale->getSaleItems() as $item) {
                $total += $item->getQuantity() * $item->getUnitPrice();
            }
            $sale->setTotalAmount($total);

            $em->flush();

            $this->addFlash('success', 'Vente modifiée avec succès !');
            return $this->redirectToRoute('app_sale_show', ['id' => $sale->getId()]);
        }

        return $this->render('sale/edit.html.twig', [
            'page_title' => 'Modifier la Vente',
            'form' => $form,
            'sale' => $sale,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_sale_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Sale $sale, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sale->getId(), $request->request->get('_token'))) {
            $em->remove($sale);
            $em->flush();

            $this->addFlash('success', 'Vente supprimée avec succès !');
        }

        return $this->redirectToRoute('app_sale_index');
    }

    #[Route('/api/product/{id}', name: 'app_sale_product_info', methods: ['GET'])]
    public function getProductInfo(int $id, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        return new JsonResponse([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'stock' => $product->getStockQuantity(),
        ]);
    }
}