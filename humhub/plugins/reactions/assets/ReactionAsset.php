<?php

namespace humhub\modules\reactions\assets;

use humhub\components\assets\AssetBundle;

class ReactionAsset extends AssetBundle
{
    public $sourcePath = '@reactions/resources';

    public $js = [
        'js/humhub.reactions.js',
    ];
}
