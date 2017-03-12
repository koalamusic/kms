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
      datas: []
    }
  },

  computed: {
    displayedItems () {
      return limitBy(
        filterBy(this.datas, this.q, 'name'),
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
      this.sortItems()
    },
    sortItems() {
      this.datas = orderBy(this.datas, this.sorting.key, this.sorting.reverse)
    },
    init() {
      var self = this
      artistStore.init().then(function(artists) {
        self.datas = artists
        self.sortItems()
      }).catch(function() {
        console.log("Artists loading error")
      })
    }
  },

  created () {
    this.init()

    event.on({
      'filter:changed': q => {
        this.q = q
      }
    })
  },

  destroyed () {
    this.datas = []
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
