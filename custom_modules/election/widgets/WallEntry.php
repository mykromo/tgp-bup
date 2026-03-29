<?php

namespace humhub\modules\election\widgets;

use humhub\modules\content\widgets\stream\WallStreamEntryWidget;

class WallEntry extends WallStreamEntryWidget
{
    public $editRoute = '';

    public function renderContent()
    {
        return $this->render('wallEntry', [
            'election' => $this->model,
        ]);
    }
}
