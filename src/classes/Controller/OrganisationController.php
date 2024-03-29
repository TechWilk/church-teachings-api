<?php
namespace TechWilk\Church\Teachings\Controller;

use TechWilk\Church\Teachings\Model\Organisation;

class OrganisationController extends AbstractController
{
    public function findOrganisations()
    {
        return 'something';
    }

    public function getExistingOrganisations($request, $response, $args)
    {
        $filters = $request->getQueryParam('filter');

        if (array_key_exists('slug', $filters)) {
            $organisation = Organisation::query()->where('slug', '=', $filters['slug'])->firstOrFail();

            return $response->withJson(['data' => [$organisation]]);
        }

        $organisations = Organisation::get();

        return $response->withJson(['data' => $organisations]);
    }

    public function getExistingOrganisation($request, $response, $args)
    {
        $organisation = Organisation::query()->find($args['id']);

        
        return $response->withJson(['data' => $organisation]);
    }

    public function postCreateOrganisation($request, $response, $args)
    {
        return $response;
    }
}
