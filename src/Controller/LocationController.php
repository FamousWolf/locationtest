<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LocationController extends AbstractController
{
    #[Route('/', name: 'app_location')]
    public function index(): Response
    {
        return $this->render('location/index.html.twig', [
            'controller_name' => 'LocationController',
        ]);
    }

    #[Route('/search', name: 'app_location_search')]
    public function search(Request $request, LocationRepository $locationRepository): JsonResponse
    {
        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');
        $distance = $request->query->get('distance');

        // @todo Validate parameters

        return $this->json([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'distance' => $distance,
            'results' => $locationRepository->findByCoordinatesAndDistance($latitude, $longitude, $distance),
        ]);
    }
}
