<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use JWTAuth;

class PostResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray($request): array
  {
    $data = [
      'id' => $this->id,
      'title' => $this->title,
      'description' => $this->description,
      'user' => $this->user,
    ];

    if (JWTAuth::user() !== null) {
      $data['body'] = $this->body;
    }

    return $data;
  }
}
