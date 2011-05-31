<?php

error_reporting(0);
#$modx->log(modX::LOG_LEVEL_INFO,'An information message in normal colors.');
#$modx->log(modX::LOG_LEVEL_ERROR,'An error in red!');
#$modx->log(modX::LOG_LEVEL_WARN,'A warning in blue!');

/*  */
$context_key = $modx->getOption('context_key', $scriptProperties, 'web');
$template = $modx->getOption('template', $scriptProperties, 1);
$published = $modx->getOption('published', $scriptProperties, 1);

$wn_menutitle   = $modx->getOption('wn_menutitle',   $scriptProperties, 2);
$wn_pagetitle   = $modx->getOption('wn_pagetitle',   $scriptProperties, 4);
$wn_longtitle   = $modx->getOption('wn_longtitle',   $scriptProperties, 8);
$wn_description = $modx->getOption('wn_description', $scriptProperties, 16);
$wn_intotext    = $modx->getOption('wn_intotext',    $scriptProperties, 32);
$wn_content     = $modx->getOption('wn_content',     $scriptProperties, 256);

$number = $modx->getOption('number', $scriptProperties, 1);
$wordspp = $modx->getOption('wordspp', $scriptProperties, 100);
$depth = $modx->getOption('depth', $scriptProperties, 3);
$dices = $modx->getOption('dices', $scriptProperties, 3);

/*  */
if(!$modx->loadClass('lib.LoremIpsumGenerator', $modx->loremipsum->config['corePath'], true, true)) {
    return $modx->error->failure('Could not load class lib.LoremIpsumGenerator');
}
#require_once($modx->loremipsum->config['corePath'].'lib/loremipsumgenerator.class.php');
require_once($modx->loremipsum->config['corePath'].'lib/random.php');

/*  */
$generator = new LoremIpsumGenerator($wordspp);

for($i=0; $i<$number; $i++) {

    /*  */
    if(lcg_rand_ndn($dices)>0.25) {
        $query = $modx->newQuery('modResource');
        $query->where(array(
            'isfolder' => '1',
            'template' => $template,
            'context_key' => $context_key
        ));
        $query->select($modx->getSelectColumns('modResource', '', '', array('id')));
        $num = $modx->getCount('modResource', $query);
        $query->limit(1, round(lcg_rand_ndn(1)*$num));
        $parent = $modx->getObject('modResource', $query);
        if($parent) {
            $parent = $parent->get('id');
        }
    } else {
        $parent = 0;
    }

    /*  */
    $resource = $modx->newObject('modResource');
    $resource->fromArray(array(
        'context_key' => $context_key,
        'template' => $template,
        'published' => $published,
        'parent' => (int) $parent,
        'isfolder' => max(0, floor(lcg_rand_ndn($dices)+0.3)),
        'menutitle'   => $generator->getContent(round($wn_menutitle*(lcg_rand_ndn($dices)+0.5)), 'plain', false),
        'pagetitle'   => $generator->getContent(round($wn_pagetitle*(lcg_rand_ndn($dices)+0.5)), 'plain', false),
        'longtitle'   => $generator->getContent(round($wn_longtitle*(lcg_rand_ndn($dices)+0.5)), 'plain', false),
        'description' => $generator->getContent(round($wn_description*(lcg_rand_ndn($dices)+0.5)), 'plain', false),
        'intotext'    => $generator->getContent(round($wn_intotext*(lcg_rand_ndn($dices)+0.5)), 'plain', false),
        'content'     => $generator->getContent(round($wn_content*(lcg_rand_ndn($dices)+0.5)), 'plain', false),
    ));

    /*  */
    if($resource->save()) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Resource '.$resource->get('id').' created.');
    } else {
        return $modx->error->failure('Could not save resource.');
    }
}
return $modx->error->success();
