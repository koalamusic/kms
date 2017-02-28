<template>
  <section id="artistsWrapper">
    <h1 class="heading">
      <span>Artists</span>
      <sort-mode-switch :mode="sorting" for="artists"/>
      &nbsp;
      <view-mode-switch :mode="viewMode" for="artists"/>
    </h1>

    <div class="artists main-scroll-wrap" :class="'as-'+viewMode" @scroll="scrolling">
      <artist-item v-for="item in displayedItems" :artist="item"/>
      <span class="item filler" v-for="n in 6"/>
      <to-top-button/>
    </div>
  </section>
</template>

<script>
import { filterBy, limitBy, orderBy, event } from '../../../utils'
import { artistStore } from '../../../stores'

import artistItem from '../../shared/artist-item.vue'
import viewModeSwitch from '../../shared/view-mode-switch.vue'
import sortModeSwitch from '../../shared/sort-mode-switch.vue'
import infiniteScroll from '../../../mixins/infinite-scroll'

export default {
  mixins: [infiniteScroll],

  components: { artistItem, viewModeSwitch, sortModeSwitch },

  data () {
    return {
      perPage: 21,
      numOfItems: 21,
      q: '',
      viewMode: null,
      sorting: {
        key: null,
        reverse: false
      },
      datas: [],
      reload: true
    }
  },

  computed: {
    displayedItems () {
      this.loadItems()

      return limitBy(
        this.datas,
        this.numOfItems
      )
    }
  },

  methods: {
    changeViewMode (mode) {
      this.viewMode = mode
    },
    changeSortMode (sort, reverse = false) {
      this.sorting.key = sort
      this.sorting.reverse = reverse
      this.reload = true
    },
    loadItems(force = false) {
      if(this.reload || force) {
        this.datas = filterBy(artistStore.all, this.q, 'name')
        this.datas = orderBy(this.datas, this.sorting.key, this.sorting.reverse)

        this.reload = false
      }
    }
  },

  created () {
    event.on({
      /**
       * When the application is ready, load the first batch of items.
       */
      'koel:ready': () => this.loadItems(true),

      'filter:changed': q => {
        this.q = q,
        this.reload = true
      }
    })
  }
}
</script>

<style lang="scss">
@import "../../../../sass/partials/_vars.scss";
@import "../../../../sass/partials/_mixins.scss";

#artistsWrapper {
  .artists {
    @include artist-album-wrapper();
  }
}
</style>
