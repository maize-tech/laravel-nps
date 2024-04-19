<?php

namespace Maize\Nps\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maize\Nps\Models\Nps;

class NpsAnswerDelayController extends Controller
{
    public function __invoke(Request $request, $id)
    {
        $user = $request->user();

        /** @phpstan-ignore-next-line */
        $user->delayNps(
            Nps::findOrFail($id),
        );

        return response()->noContent();
    }
}
