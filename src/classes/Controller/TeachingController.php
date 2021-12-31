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

        if (array_key_exists('passage', $filters)) {
            $passages = $filters['passage'];

            foreach ($passages as $passage) {
                [$from, $to] = explode(',', $passage);
                $teachings = Teaching::whereHas('passages', function (Builder $query) use ($from, $to) {
                    $query->where('passage_from', '>=', $from)->where('passage_to', '<=', $to);
                })->get();
            }
        } else {
            $teachings = Teaching::get();
        }


        foreach($teachings as &$teaching) {
            $teaching->passages = $teaching->passages()->get();
        }

        return $response->withJson(['data' => $teachings]);
    }

    public function getExistingTeaching($request, $response, $args)
    {
        $teaching = Teaching::query()->where('slug', '=', $args['slug'])->firstOrFail();

        $teaching->passages = $teaching->passages()->get();
        
        return $response->withJson($teaching);
    }

    public function postCreateTeaching($request, $response, $args)
    {
        return $response;
    }
}
