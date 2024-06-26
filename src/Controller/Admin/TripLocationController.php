<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\TripLocationData;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Model\Event\Admin\TripLocation\TripLocationCreateEvent;
use App\Model\Event\Admin\TripLocation\TripLocationDeleteEvent;
use App\Model\Event\Admin\TripLocation\TripLocationUpdateEvent;
use App\Model\Repository\TripLocationPathRepositoryInterface;
use App\Model\Repository\TripLocationRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\TripLocationType;
use App\Service\Form\Type\Common\HiddenTrueType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
#[IsGranted('trip_location_path_update')]
class TripLocationController extends AbstractController
{
    private TripLocationRepositoryInterface $tripLocationRepository;
    private TripLocationPathRepositoryInterface $tripLocationPathRepository;

    public function __construct(TripLocationRepositoryInterface     $tripLocationRepository,
                                TripLocationPathRepositoryInterface $tripLocationPathRepository)
    {
        $this->tripLocationRepository = $tripLocationRepository;
        $this->tripLocationPathRepository = $tripLocationPathRepository;
    }

    #[Route('/admin/trip-location-path/{id}/create-location', name: 'admin_trip_location_create')]
    public function create(EventDispatcherInterface      $eventDispatcher,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $tripLocationPath = $this->findTripLocationPathOrThrow404($id);
        $tripLocationData = new TripLocationData(null, $tripLocationPath);

        $form = $this->createForm(TripLocationType::class, $tripLocationData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.trip_location.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new TripLocationCreateEvent($tripLocationData, $tripLocationPath);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.trip_location.create');

            return $this->redirectToRoute('admin_trip_location_path_update', [
                'id' => $tripLocationPath->getId()->toRfc4122(),
            ]);
        }

        return $this->render('admin/trip/location/update.html.twig', [
            'form_trip_location' => $form->createView(),
            'breadcrumbs'        => $this->createBreadcrumbs([
                'trip_location_path' => $tripLocationPath,
            ]),
        ]);
    }

    #[Route('/admin/trip-location/{id}/read', name: 'admin_trip_location_read')]
    public function read(UuidV4 $id): Response
    {
        $tripLocation = $this->findTripLocationOrThrow404($id);
        $tripLocationPath = $tripLocation->getTripLocationPath();

        return $this->render('admin/trip/location/read.html.twig', [
            'trip_location' => $tripLocation,
            'breadcrumbs'   => $this->createBreadcrumbs([
                'trip_location_path' => $tripLocationPath,
                'trip_location'      => $tripLocation,
            ]),
        ]);
    }

    #[Route('/admin/trip-location/{id}/update', name: 'admin_trip_location_update')]
    public function update(EventDispatcherInterface      $eventDispatcher,
                           DataTransferRegistryInterface $dataTransfer,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $tripLocation = $this->findTripLocationOrThrow404($id);
        $tripLocationPath = $tripLocation->getTripLocationPath();
        $tripLocationData = new TripLocationData($tripLocation, $tripLocationPath);

        $dataTransfer->fillData($tripLocationData, $tripLocation);
        $form = $this->createForm(TripLocationType::class, $tripLocationData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.trip_location.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new TripLocationUpdateEvent($tripLocationData, $tripLocation);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.trip_location.update');

            return $this->redirectToRoute('admin_trip_location_path_update', [
                'id' => $tripLocationPath->getId()->toRfc4122(),
            ]);
        }

        return $this->render('admin/trip/location/update.html.twig', [
            'form_trip_location' => $form->createView(),
            'breadcrumbs'        => $this->createBreadcrumbs([
                'trip_location_path' => $tripLocationPath,
                'trip_location'      => $tripLocation,
            ]),
        ]);
    }

    #[Route('/admin/trip-location/{id}/delete', name: 'admin_trip_location_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $tripLocation = $this->findTripLocationOrThrow404($id);
        $tripLocationPath = $tripLocation->getTripLocationPath();

        if (!$this->tripLocationRepository->canRemoveTripLocation($tripLocation))
        {
            $this->addTransFlash('failure', 'crud.error.trip_location_delete');

            return $this->redirectToRoute('admin_trip_location_path_update', [
                'id' => $tripLocationPath->getId()->toRfc4122(),
            ]);
        }

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.trip_location_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new TripLocationDeleteEvent($tripLocation);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.trip_location.delete');

            return $this->redirectToRoute('admin_trip_location_path_update', [
                'id' => $tripLocationPath->getId()->toRfc4122(),
            ]);
        }

        return $this->render('admin/trip/location/delete.html.twig', [
            'trip_location' => $tripLocation,
            'form_delete'   => $form->createView(),
            'breadcrumbs'   => $this->createBreadcrumbs([
                'trip_location_path' => $tripLocationPath,
                'trip_location'      => $tripLocation,
            ]),
        ]);
    }

    private function findTripLocationOrThrow404(UuidV4 $id): TripLocation
    {
        $tripLocation = $this->tripLocationRepository->findOneById($id);
        if ($tripLocation === null)
        {
            throw $this->createNotFoundException();
        }

        return $tripLocation;
    }

    private function findTripLocationPathOrThrow404(UuidV4 $id): TripLocationPath
    {
        $tripLocationPath = $this->tripLocationPathRepository->findOneById($id);
        if ($tripLocationPath === null)
        {
            throw $this->createNotFoundException();
        }

        return $tripLocationPath;
    }
}