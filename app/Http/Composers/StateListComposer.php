<?php namespace App\Http\Composers;

use App\Core\State;
use Illuminate\Contracts\View\View;

class StateListComposer
{
    /**
     * @param View $view
     * @return $this
     */
    public function compose(View $view)
    {
        return $view->with([
            'states' => State::pluck('abbreviation')->prepend('State')
        ]);
    }
}