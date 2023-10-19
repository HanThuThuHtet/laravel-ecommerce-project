<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

     public function stockStatus($count){
        $status = "";
        if($count > 10){
            $status = "Instock";
        }else if($count > 0){
            $status = "Low Stock";
        }else if($count == 0){
            $status = "Out of Stock";
        }
        return $status;
     }
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            "id" => $this->id,
            "name" => $this->name,
            "price" => $this->price,
            "show_pirce" => $this->price." mmk",
            "stock" => $this->stock,
            "stock_status" => $this->stockStatus($this->stock),
            "date" => $this->created_at->format("d M Y"),
            "time" => $this->created_at->format("H:i A"),
            //"owner" => $this->user->name,
            "owner" => new UserResource($this->user),
            "photos" => PhotoResource::collection($this->photos)
        ];
    }
}
