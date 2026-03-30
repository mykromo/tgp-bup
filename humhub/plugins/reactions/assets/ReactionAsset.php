<?php

namespace humhub\modules\reactions\assets;

use humhub\components\assets\AssetBundle;

class ReactionAsset extends AssetBundle
{
    public $sourcePath = '@reactions/resources';

    public $css = [
        'css/reactions.css',
    ];

    public $js = [
        'js/humhub.reactions.js',
    ];
}
