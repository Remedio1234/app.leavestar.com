<template>
    <ol class="siblings">
        <li v-for="item in sibling"  >
           <div class="menuDiv">
              <div  class="menuEdit">
                   <p>  {{item.name}}</p>
              </div>
          </div>
        </li>
    </ol>
</template>

<script>
    export default {
        name: 'SiblingTree',
        props: {
            sibling: Array,
        },

    }
</script>
