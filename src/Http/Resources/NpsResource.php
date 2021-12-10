<?php

namespace Maize\Nps\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class NpsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id, /** @phpstan-ignore-line */
            'values' => $this->values, /** @phpstan-ignore-line  */
            'question' => $this->question, /** @phpstan-ignore-line */
        ];
    }
}
