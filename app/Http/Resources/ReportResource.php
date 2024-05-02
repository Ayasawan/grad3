<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            "id"=>$this->id,
            "pdf"=>$this->pdf,
            "report_date"=>$this->report_date,
            "project_id"=>$this->Project()->get(),
            "user_id"=>$this->User()->get(),
        ];

    }
}
