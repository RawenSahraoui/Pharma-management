<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Form\SupplierType;
use App\Repository\SupplierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/supplier')]
class SupplierController extends AbstractController
{
    #[Route('/', name: 'app_supplier_index')]
    public function index(
        Request $request,
        SupplierRepository $supplierRepository,
        PaginatorInterface $paginator
    ): Response {
        // Get search parameter from query string
        $search = $request->query->get('search', '');

        // Build query based on search
        $queryBuilder = $supplierRepository->createQueryBuilder('s');
        
        if ($search) {
            $queryBuilder
                ->where('s.name LIKE :search')
                ->orWhere('s.email LIKE :search')
                ->orWhere('s.phone LIKE :search')
                ->orWhere('s.city LIKE :search')
                ->orWhere('s.contactPerson LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Paginate results
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10 // items per page
        );

        return $this->render('supplier/index.html.twig', [
            'page_title' => 'Fournisseurs',
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'app_supplier_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $supplier = new Supplier();
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($supplier);
            $em->flush();

            $this->addFlash('success', 'Fournisseur créé avec succès !');
            return $this->redirectToRoute('app_supplier_index');
        }

        return $this->render('supplier/new.html.twig', [
            'page_title' => 'Nouveau Fournisseur',
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_supplier_show', requirements: ['id' => '\d+'])]
    public function show(Supplier $supplier): Response
    {
        return $this->render('supplier/show.html.twig', [
            'page_title' => 'Détails du Fournisseur',
            'supplier' => $supplier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_supplier_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Supplier $supplier, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Fournisseur modifié avec succès !');
            return $this->redirectToRoute('app_supplier_index');
        }

        return $this->render('supplier/edit.html.twig', [
            'page_title' => 'Modifier le Fournisseur',
            'form' => $form,
            'supplier' => $supplier,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_supplier_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Supplier $supplier, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $supplier->getId(), $request->request->get('_token'))) {
            $em->remove($supplier);
            $em->flush();

            $this->addFlash('success', 'Fournisseur supprimé avec succès !');
        }

        return $this->redirectToRoute('app_supplier_index');
    }
}