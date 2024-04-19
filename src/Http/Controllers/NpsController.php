<?php

namespace Maize\Nps\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maize\Nps\Http\Resources\NpsResource;

class NpsController extends Controller
{
    public function __invoke(Request $request)
    {
        $nps = $request->user();

        /** @phpstan-ignore-next-line */
        $nps->findCurrentNps(true);

        return NpsResource::make($nps);
    }
}
