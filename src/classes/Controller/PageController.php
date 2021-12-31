<?php
namespace TechWilk\Church\Teachings\Controller;

use TechWilk\Church\Teachings\Model\Organisation;
use TechWilk\Church\Teachings\Model\Teaching;

class PageController extends AbstractController
{
    public function findPages()
    {
        return 'something';
    }

    public function getExistingTeaching($request, $response, $args)
    {
        $organisation = Organisation::query()->where('slug', '=', $args['teachingSlug'])->firstOrFail();
        $teaching = Teaching::query()
            ->where('organisation_id', '=', $organisation->id)    
            ->where('slug', '=', $args['teachingSlug'])
            ->firstOrFail();
        
        return $response->withJson([
            'teaching' => $teaching,
            'organisation' => $organisation,
        ]);
    }
}
