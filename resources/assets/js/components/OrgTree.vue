<template>
<section id="demo" >
    <ol class="sortable ui-sortable mjs-nestedSortable-branch mjs-nestedSortable-expanded">
        <tree-node v-for="item in JsonTree" :item="item" :level="1"></tree-node>
    </ol>

    <!--<tree-subling :sibling="SibTree"></tree-subling>-->
    <button id="toHierarchy" @click="UpdateTree" class="btn btn-primary">save</button>
    <a class="btn btn-primary" onclick= "return confirm('Are you sure?')" href="/home/terminateAccount">Terminate Account</a>
</section>
</template>

<script>
    import TreeNode from './TreeNode.vue'
    import SiblingTree from './SiblingTree.vue'

    export default {
        name: 'OrgTree',
        components: {
            TreeNode,
            SiblingTree,

        },
        props: {
            tree: String,
            //sibling: String,
        },
        data() {
            return {
                JsonTree: JSON.parse(this.tree),
                //SibTree: JSON.parse(this.sibling),
            }
        },
        methods: {
            dump: function(arr, level) {
                var dumped_text = ""
                if (!level) level = 0
                //The padding given at the beginning of the line.
                var level_padding = ""
                for (var j = 0; j < level + 1; j++) level_padding += "    "
                if (typeof(arr) == 'object') { //Array/Hashes/Objects
                    for (var item in arr) {
                        var value = arr[item]

                        if (typeof(value) == 'object') { //If it is an array,
                            dumped_text += level_padding + "'" + item + "' ...\n"
                            dumped_text += this.dump(value, level + 1)
                        } else {
                            dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n"
                        }
                    }
                } else { //Strings/Chars/Numbers etc.
                    dumped_text = "===>" + arr + "<===(" + typeof(arr) + ")"
                }
                return dumped_text
            },

            UpdateTree: function() {
                var hiered = $('ol.sortable').nestedSortable('toHierarchy', {
                    startDepthCount: 0
                });
                $('#form_value').val(JSON.stringify(hiered));
                $('#form_submit').submit();
            },

        }


    }
</script>
