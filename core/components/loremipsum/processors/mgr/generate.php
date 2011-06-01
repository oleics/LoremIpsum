<?php

error_reporting(0);
#$modx->log(modX::LOG_LEVEL_INFO,'An information message in normal colors.');
#$modx->log(modX::LOG_LEVEL_ERROR,'An error in red!');
#$modx->log(modX::LOG_LEVEL_WARN,'A warning in blue!');

/*  */
$context = $modx->getOption('context', $scriptProperties, 'web');
$template = $modx->getOption('template', $scriptProperties, 1);
$parent = $modx->getOption('parent', $scriptProperties, 0);
$published = $modx->getOption('published', $scriptProperties, 0);

$now_menutitle   = $modx->getOption('now_menutitle',   $scriptProperties, 2);
$now_pagetitle   = $modx->getOption('now_pagetitle',   $scriptProperties, 4);
$now_longtitle   = $modx->getOption('now_longtitle',   $scriptProperties, 8);
$now_description = $modx->getOption('now_description', $scriptProperties, 16);
$now_introtext   = $modx->getOption('now_introtext',   $scriptProperties, 32);
$now_content     = $modx->getOption('now_content',     $scriptProperties, 256);

$number_of_resources = $modx->getOption('number_of_resources', $scriptProperties, 1);
$depth = $modx->getOption('depth', $scriptProperties, 3);
$wordspp = $modx->getOption('wordspp', $scriptProperties, 100);

$dices = $modx->getOption('dices', $scriptProperties, 3);
$probability_isfolder = (float) $modx->getOption('probability_isfolder', $scriptProperties, 0.1);
$probability_hasparent = (float) $modx->getOption('probability_hasparent', $scriptProperties, 0.9);

/* check if context exists */
$context = $modx->getObject('modContext', $context);
if(!$context) {
    $modx->error->addField('context', $modx->lexicon('err_nf'));
    $modx->log(modX::LOG_LEVEL_ERROR, 'Context not found.');
    return $modx->error->failure();
}

/* check if template exists */
$template = $modx->getObject('modTemplate', $template);
if(!$template) {
    $modx->error->addField('template', $modx->lexicon('err_nf'));
    $modx->log(modX::LOG_LEVEL_ERROR, 'Template not found.');
    return $modx->error->failure();
}

/* check if parent exists */
$tmp = $modx->getObject('modResource', $parent);
if(!$tmp) {
    $modx->error->addField('parent', $modx->lexicon('err_nf'));
    $modx->log(modX::LOG_LEVEL_ERROR, 'Parent not found.');
    return $modx->error->failure();
}
unset($tmp);

/*  */
if(!$modx->loadClass('lib.LoremIpsumGenerator', $modx->loremipsum->config['corePath'], true, true)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not load class lib.LoremIpsumGenerator');
    return $modx->error->failure();
}
$generator = new LoremIpsumGenerator($wordspp);
#require_once($modx->loremipsum->config['corePath'].'lib/loremipsumgenerator.class.php');

/*  */
require_once($modx->loremipsum->config['corePath'].'lib/random.php');

/*  */
for($i=0; $i<$number_of_resources; $i++) {

    /*  */
    if(lcg_probability($probability_hasparent)) {
        $query = $modx->newQuery('modResource');
        $query->where(array(
            'isfolder' => '1',
            'template' => $template->get('id'),
            'context_key' => $context->get('key')
        ));
        $query->select($modx->getSelectColumns('modResource', '', '', array('id')));
        $num = $modx->getCount('modResource', $query);
        $query->limit(1, round(lcg_rand_ndn(3)*$num));
        $parent = $modx->getObject('modResource', $query);
        if($parent) {
            $parentIds = $modx->getParentIds($parent->get('id'), $depth+1);
            if(count($modx->getParentIds($parent->get('id'), $depth+1)<=$depth)) {
                $parent = $parent->get('id');
            } else {
                $parent = (int) array_shift($parentIds);
            }
        } else {
            $parent = $modx->getOption('parent', $scriptProperties, 0);
        }
    } else {
        $parent = $modx->getOption('parent', $scriptProperties, 0);
    }

    /*  */
    $resource = $modx->newObject('modResource');
    $resource->fromArray(array(
        'context_key' => $context->get('key'),
        'template' => $template->get('id'),
        'published' => $published,
        'parent' => (int) $parent,
        'isfolder' => (int) lcg_probability($probability_isfolder),
        'menutitle'   => $generator->getContent(round($now_menutitle*(lcg_rand_ndn($dices)+0.5)),   'plain', false),
        'pagetitle'   => $generator->getContent(round($now_pagetitle*(lcg_rand_ndn($dices)+0.5)),   'plain', false),
        'longtitle'   => $generator->getContent(round($now_longtitle*(lcg_rand_ndn($dices)+0.5)),   'plain', false),
        'description' => $generator->getContent(round($now_description*(lcg_rand_ndn($dices)+0.5)), 'plain', false),
        'introtext'   => $generator->getContent(round($now_introtext*(lcg_rand_ndn($dices)+0.5)),   'plain', false),
        'content'     => $generator->getContent(round($now_content*(lcg_rand_ndn($dices)+0.5)),     'plain', false),
    ));

    /*  */
    if($resource->save()) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Resource '.$resource->get('id').' created.');
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Could not save resource.');
        return $modx->error->failure();
    }
}
return $modx->error->success();
