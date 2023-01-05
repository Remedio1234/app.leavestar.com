<template>
<li id="menuItem_{{item.id}}" data-name="{{item.name}}" data-account_id="{{item.account_id}}" data-level="{{level}}">
    <div class="menuDiv">
        <div id="menuEdit{{item.id}}" class="menuEdit">
            <span style='text-align:left'>{{item.name}}</span>

            <a class="button-open-right" style="float:right;" href="javascript:return false;" data-href="/organisationStructures/create/{{item.id}}"><span class="glyphicon glyphicon-plus-sign"></span> Create </a>
            <a v-if="item.parent_id!==null"    class="button-open-right" style="float:right;" href="javascript:return false;" data-href="/organisationStructures/{{item.id}}/edit"><span class="glyphicon glyphicon-pencil"></span> Update</a>
            <a v-if="level!=1" class="" style="float:right;" href="/organisationStructures/datadelete/{{item.id}}"><span class="glyphicon glyphicon-trash"></span> Delete</a>

        </div>
    </div>
    <ol v-if="!hasChild">
        <tree-node v-for="item in item.children" :item="item" :level="level+1"></tree-node>
    </ol>
</li>
</template>

<script>
    export default {
        name: 'TreeNode',
        props: {
            item: Object,
            level: Number,
        },
        computer: {
            hasChild() {
                return item.children === null
            },

        },


    }
</script>
