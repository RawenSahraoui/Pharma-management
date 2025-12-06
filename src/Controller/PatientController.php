<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/patient')]
class PatientController extends AbstractController
{
    #[Route('/', name: 'app_patient_index')]
    public function index(
        Request $request,
        CustomerRepository $customerRepository,
        PaginatorInterface $paginator
    ): Response {
        $search = $request->query->get('search', '');

        $queryBuilder = $customerRepository->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC');

        if ($search) {
            $queryBuilder
                ->where('c.name LIKE :search')
                ->orWhere('c.email LIKE :search')
                ->orWhere('c.phone LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('patient/index.html.twig', [
            'page_title' => 'Patients',
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'app_patient_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($customer);
            $em->flush();

            $this->addFlash('success', 'Patient créé avec succès !');
            return $this->redirectToRoute('app_patient_index');
        }

        return $this->render('patient/new.html.twig', [
            'page_title' => 'Nouveau Patient',
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_patient_show', requirements: ['id' => '\d+'])]
    public function show(Customer $customer): Response
    {
        return $this->render('patient/show.html.twig', [
            'page_title' => 'Détails du Patient',
            'customer' => $customer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_patient_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Customer $customer, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Patient modifié avec succès !');
            return $this->redirectToRoute('app_patient_index');
        }

        return $this->render('patient/edit.html.twig', [
            'page_title' => 'Modifier le Patient',
            'form' => $form,
            'customer' => $customer,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_patient_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Customer $customer, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $customer->getId(), $request->request->get('_token'))) {
            $em->remove($customer);
            $em->flush();

            $this->addFlash('success', 'Patient supprimé avec succès !');
        }

        return $this->redirectToRoute('app_patient_index');
    }
}