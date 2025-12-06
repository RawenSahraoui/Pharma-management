<?php

namespace App\Controller;

use App\Entity\SupplierOrder;
use App\Form\SupplierOrderType;
use App\Repository\SupplierOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/supplier/order', name: 'app_supplier_order_')]
class SupplierOrderController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        Request $request,
        SupplierOrderRepository $orderRepository,
        PaginatorInterface $paginator
    ): Response {
        $queryBuilder = $orderRepository->createQueryBuilder('o');

        // Optional: Search/filter functionality
        $search = $request->query->get('search', '');
        if ($search) {
            $queryBuilder
                ->where('o.status LIKE :search')
                ->orWhere('o.id LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $orders = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('supplier_order/index.html.twig', [
            'page_title' => 'Commandes Fournisseurs',
            'orders' => $orders,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $order = new SupplierOrder();
        $form = $this->createForm(SupplierOrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($order);
            $em->flush();

            $this->addFlash('success', 'Commande fournisseur créée avec succès !');

            return $this->redirectToRoute('app_supplier_order_index');
        }

        return $this->render('supplier_order/new.html.twig', [
            'page_title' => 'Nouvelle Commande',
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(SupplierOrder $order): Response
    {
        return $this->render('supplier_order/show.html.twig', [
            'page_title' => 'Détails de la Commande',
            'order' => $order,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, SupplierOrder $order, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SupplierOrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Commande fournisseur modifiée avec succès !');

            return $this->redirectToRoute('app_supplier_order_index');
        }

        return $this->render('supplier_order/edit.html.twig', [
            'page_title' => 'Modifier la Commande',
            'form' => $form,
            'order' => $order,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, SupplierOrder $order, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $em->remove($order);
            $em->flush();

            $this->addFlash('success', 'Commande fournisseur supprimée avec succès !');
        }

        return $this->redirectToRoute('app_supplier_order_index');
    }
}
