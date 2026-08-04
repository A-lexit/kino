<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FilmResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

		return [
            'id' => $this->id,
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'statistic' => new StateResource($this->whenLoaded('state')),
        ];
    }

}
