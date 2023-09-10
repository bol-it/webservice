<template>
  <div v-show="is_visible" style="transform-origin: center center 0px;">
    <v-btn
      v-scroll="onScroll"
      class="mx-2"
      large
      fab
      color="blue-grey darken-5"
      dark
      right
      bottom
      fixed
      @click="$vuetify.goTo(target, options)"
    >
      <v-icon>mdi-arrow-up-bold</v-icon>
    </v-btn>

  </div>
</template>

<style>
</style>
<script>
import * as easings from 'vuetify/es5/services/goto/easing-patterns'

  export default {
    props: {
        mainpath: String,
     },
    data () {
      return {
        data: [],
        type: 'number',
        number: 1,
        is_visible: false,
        duration: 300,
        offset: 0,
        scrolltop: 0,
        easing: 'easeInOutCubic',
      }
    },
    computed: {
      target () {
        const value = this[this.type]
        if (!isNaN(value)) return Number(value)
        else return value
      },
      options () {
        return {
          duration: this.duration,
          offset: this.offset,
          easing: this.easing,
        }
      },
    },
    created () {
      this.init();
    },
     methods: {
      init()
      {

      },
      onScroll (e) {
        //console.log(window.scrollY);
        this.scrolltop = window.scrollY;
        if (this.scrolltop > 200)
        {
         this.is_visible = true;
        }
        else
        {
         this.is_visible = false;
        }
      },
    },
  }
</script>