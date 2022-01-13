<?php
namespace TechWilk\Church\Teachings\Controller;

use Illuminate\Database\Eloquent\Builder;
use TechWilk\Church\Teachings\Model\Teaching;

class TeachingController extends AbstractController
{
    public function findTeachings()
    {
        return 'something';
    }

    public function getExistingTeachings($request, $response, $args)
    {
        $filters = $request->getQueryParam('filter') ?? [];

        $teachingsQuery = Teaching::query();

        if (array_key_exists('passage', $filters)) {
            $passages = $filters['passage'];

            foreach ($passages as $passage) {
                [$from, $to] = explode(',', $passage);

                $teachingsQuery = $teachingsQuery->whereHas('passages', function (Builder $query) use ($from, $to) {
                    $query->where('passage_from', '>=', $from)->where('passage_to', '<=', $to);
                });
            }
        }
        if (array_key_exists('slug', $filters)) {
            $slug = $filters['slug'];

            $teachingsQuery = $teachingsQuery->where('slug', '=', $slug);   
        }
        if(array_key_exists('organiser.slug', $filters)) {
            $organiserSlug = $filters['organiser.slug']; // organisation.slug

            $teachingsQuery = $teachingsQuery->whereHas('organiser', function (Builder $query) use ($organiserSlug) {
                $query->where('slug', '=', $organiserSlug);
            });
        }
        if(array_key_exists('organiser.id', $filters)) {
            $organiserId = $filters['organiser.id']; // organisation.slug

            $teachingsQuery = $teachingsQuery->where('organiser_id', '=', $organiserId);
        }

        $teachings = $teachingsQuery->get();

        foreach($teachings as &$teaching) {
            $teaching->passages = $teaching->passages()->get();
        }

        return $response->withJson(['data' => $teachings]);
    }

    public function getExistingTeaching($request, $response, $args)
    {
        $teaching = Teaching::query()->where('id', '=', $args['id'])->firstOrFail();

        $teaching->passages = $teaching->passages()->get();
        
        return $response->withJson($teaching);
    }

    public function postCreateTeaching($request, $response, $args)
    {
        return $response;
    }
}
