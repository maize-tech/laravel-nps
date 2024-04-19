<?php

namespace Maize\Nps\Http\Controllers;

use Illuminate\Routing\Controller;
use Maize\Nps\Http\Requests\NpsAnswerRequest;
use Maize\Nps\Models\Nps;

class NpsAnswerController extends Controller
{
    public function __invoke(NpsAnswerRequest $request, $id)
    {
        $user = $request->user();

        /** @phpstan-ignore-next-line */
        $user->answerNps(
            Nps::findOrFail($id),
            $request->validated()
        );

        return response()->noContent();
    }
}
