<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/location')]
class LocationController extends AbstractController
{
    #[Route('', name: 'app_location_index', methods: ['GET'])]
    public function index(LocationRepository $repository): Response
    {
        return $this->render('location/index.html.twig', [
            'locations' => $repository->findBy([], ['id' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_location_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $location = new Location();

        // Przy dodawaniu użyj grupy new.
        $form = $this->createForm(LocationType::class, $location, [
            'validation_groups' => ['Default', 'new'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($location);
            $em->flush();

            $this->addFlash('success', 'Dodano lokalizację.');

            return $this->redirectToRoute(
                'app_location_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('crud/form.html.twig', [
            'form' => $form,
            'heading' => 'Dodaj lokalizację',
            'back_route' => 'app_location_index',
        ]);
    }

    #[Route(
        '/{id}',
        name: 'app_location_show',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function show(
        #[MapEntity(id: 'id')]
        Location $location
    ): Response {
        return $this->render('location/show.html.twig', [
            'location' => $location,
        ]);
    }

    #[Route(
        '/{id}/edit',
        name: 'app_location_edit',
        requirements: ['id' => '\d+'],
        methods: ['GET', 'POST']
    )]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id')]
        Location $location,
        EntityManagerInterface $em
    ): Response {
        // Przy edycji użyj grupy edit.
        $form = $this->createForm(LocationType::class, $location, [
            'validation_groups' => ['Default', 'edit'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Zapisano zmiany lokalizacji.');

            return $this->redirectToRoute(
                'app_location_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('crud/form.html.twig', [
            'form' => $form,
            'heading' => 'Edytuj lokalizację',
            'back_route' => 'app_location_index',
        ]);
    }

    #[Route(
        '/{id}',
        name: 'app_location_delete',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')]
        Location $location,
        EntityManagerInterface $em,
        MeasurementRepository $measurements
    ): Response {
        // Sprawdź token zabezpieczający formularz.
        if (!$this->isCsrfTokenValid(
            'delete_location'.$location->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Nieprawidłowy token formularza.'
            );
        }

        // Miasto z pomiarami musi pozostać w bazie.
        if ($measurements->count(['location' => $location]) > 0) {
            $this->addFlash(
                'warning',
                'Najpierw usuń pomiary tej miejscowości.'
            );

            return $this->redirectToRoute(
                'app_location_show',
                ['id' => $location->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        $id = $location->getId();

        $em->remove($location);
        $em->flush();

        $this->addFlash(
            'success',
            'Usunięto lokalizację o ID '.$id.'.'
        );

        return $this->redirectToRoute(
            'app_location_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
