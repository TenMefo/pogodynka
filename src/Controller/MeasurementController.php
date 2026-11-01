<?php

namespace App\Controller;

use App\Entity\Measurement;
use App\Form\MeasurementType;
use App\Repository\MeasurementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/measurement')]
class MeasurementController extends AbstractController
{
    #[Route('', name: 'app_measurement_index', methods: ['GET'])]
    public function index(MeasurementRepository $repository): Response
    {
        return $this->render('measurement/index.html.twig', [
            'measurements' => $repository->findBy([], ['id' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_measurement_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $measurement = new Measurement();

        $form = $this->createForm(MeasurementType::class, $measurement, [
            'validation_groups' => ['Default', 'new'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($measurement);
            $em->flush();

            $this->addFlash('success', 'Dodano pomiar.');

            return $this->redirectToRoute(
                'app_measurement_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('crud/form.html.twig', [
            'form' => $form,
            'heading' => 'Dodaj pomiar',
            'back_route' => 'app_measurement_index',
        ]);
    }

    #[Route(
        '/{id}',
        name: 'app_measurement_show',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function show(
        #[MapEntity(id: 'id')]
        Measurement $measurement
    ): Response {
        return $this->render('measurement/show.html.twig', [
            'measurement' => $measurement,
        ]);
    }

    #[Route(
        '/{id}/edit',
        name: 'app_measurement_edit',
        requirements: ['id' => '\d+'],
        methods: ['GET', 'POST']
    )]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id')]
        Measurement $measurement,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(MeasurementType::class, $measurement, [
            'validation_groups' => ['Default', 'edit'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Zapisano zmiany pomiaru.');

            return $this->redirectToRoute(
                'app_measurement_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('crud/form.html.twig', [
            'form' => $form,
            'heading' => 'Edytuj pomiar',
            'back_route' => 'app_measurement_index',
        ]);
    }

    #[Route(
        '/{id}',
        name: 'app_measurement_delete',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')]
        Measurement $measurement,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isCsrfTokenValid(
            'delete_measurement'.$measurement->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Nieprawidłowy token formularza.'
            );
        }

        $id = $measurement->getId();

        $em->remove($measurement);
        $em->flush();

        $this->addFlash(
            'success',
            'Usunięto pomiar o ID '.$id.'.'
        );

        return $this->redirectToRoute(
            'app_measurement_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
