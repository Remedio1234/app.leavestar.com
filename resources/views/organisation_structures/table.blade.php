 
<?php

//Traverse Tree Function 
function traverse($categories, &$tree, $level, $parent) {
    foreach ($categories as $category) {

        array_push($tree, array(
            'id' => $category->id,
            'name' => $category->name,
            'level' => $level,
            'account_id' => $category->account_id,
            'parent_id' => ((isset($parent->id)) ? $parent->id : ""),
            'children' => [],
        ));

        if ($category->children->count() != 0) {
            traverse($category->children, $tree[count($tree) - 1]['children'], $level + 1, $category);
        }
    }
}

//Normal Manager Level
$realBoss = App\Models\OrganisationUser::getAccountLevel(\Auth::user()->id, $org_id);

$current_node = \App\Models\OrganisationStructure::where('id', $realBoss)->first();

$nodes = \App\Models\OrganisationStructure::descendantsOf($realBoss)->toTree();

$tree[0] = [
    'id' => $current_node->id,
    'name' => $current_node->name,
    'level' => 0,
    'account_id' => $current_node->account_id,
    'parent_id' => $current_node->parent_id,
    'children' => [],
];
traverse($nodes, $tree[0]['children'], 1, $current_node);


//    if ($current_node->parent_id !== 1) {
//        $tree_sibling = [];
//        $siblings = $current_node->getSiblings()->toTree();
//        traverse($siblings, $tree_sibling, 0, "");
//    } else {
//        $tree_sibling = [];
//    }
?>

<org-tree  tree='<?= json_encode($tree) ?>' sibling='<?php //echo json_encode($tree_sibling)                        ?>' ></org-tree>
<form action="<?= url('/organisationStructures/dataupdate') ?>" id="form_submit" method="post">
    <input type="hidden" id="form_value" name="newtree">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
</form>

 