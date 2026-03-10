<?php

namespace Core\Categories\Controllers;

use App\Http\Controllers\Controller;
use Core\Categories\Services\SlidersService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SliderRedirectController extends Controller
{
  public function __construct(protected SlidersService $slidersService)
  {
  }

  /**
   * Increment the view count for the slider link UUID and redirect to the real URL.
   */
  public function redirect(string $uuid)
  {
    try {
      $url = $this->slidersService->redirect($uuid);
      return redirect()->away($url);
    } catch (ModelNotFoundException $e) {
      abort(404);
    }
  }
}
