<?php

namespace App\Controller;

use App\Entity\Prescription;
use App\Form\PrescriptionType;
use App\Repository\PrescriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/prescription')]
class PrescriptionController extends AbstractController
{
    #[Route('/', name: 'app_prescription_index')]
    public function index(
        Request $request,
        PrescriptionRepository $prescriptionRepository,
        PaginatorInterface $paginator
    ): Response {
        $search = $request->query->get('search', '');
        $status = $request->query->get('status', '');

        $queryBuilder = $prescriptionRepository->createQueryBuilder('p')
            ->orderBy('p.prescriptionDate', 'DESC');

        if ($search) {
            $queryBuilder
                ->leftJoin('p.customer', 'c')
                ->leftJoin('p.doctor', 'd')
                ->andWhere('p.prescriptionNumber LIKE :search OR c.name LIKE :search OR d.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($status) {
            $queryBuilder
                ->andWhere('p.status = :status')
                ->setParameter('status', $status);
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('prescription/index.html.twig', [
            'page_title' => 'Prescriptions',
            'pagination' => $pagination,
            'search' => $search,
            'status' => $status,
        ]);
    }

    #[Route('/new', name: 'app_prescription_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $prescription = new Prescription();
        $prescription->setPrescriptionDate(new \DateTime());
        
        $form = $this->createForm(PrescriptionType::class, $prescription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($prescription->getPrescriptionItems() as $item) {
                $item->setPrescription($prescription);
            }

            $em->persist($prescription);
            $em->flush();

            $this->addFlash('success', 'Prescription créée avec succès !');
            return $this->redirectToRoute('app_prescription_show', ['id' => $prescription->getId()]);
        }

        return $this->render('prescription/new.html.twig', [
            'page_title' => 'Nouvelle Prescription',
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prescription_show', requirements: ['id' => '\d+'])]
    public function show(Prescription $prescription): Response
    {
        return $this->render('prescription/show.html.twig', [
            'page_title' => 'Détails de la Prescription',
            'prescription' => $prescription,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prescription_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Prescription $prescription, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PrescriptionType::class, $prescription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Prescription modifiée avec succès !');
            return $this->redirectToRoute('app_prescription_show', ['id' => $prescription->getId()]);
        }

        return $this->render('prescription/edit.html.twig', [
            'page_title' => 'Modifier la Prescription',
            'form' => $form,
            'prescription' => $prescription,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_prescription_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Prescription $prescription, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $prescription->getId(), $request->request->get('_token'))) {
            $em->remove($prescription);
            $em->flush();

            $this->addFlash('success', 'Prescription supprimée avec succès !');
        }

        return $this->redirectToRoute('app_prescription_index');
    }
}