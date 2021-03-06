<template>
  <section id="albumsWrapper">
    <h1 class="heading">
      <span>Albums</span>
      <sort-mode-switch :mode="sorting" for="albums"/>
      &nbsp;
      <view-mode-switch :mode="viewMode" for="albums"/>
    </h1>

    <div ref="scroller" class="albums main-scroll-wrap" :class="'as-'+viewMode" @scroll="scrolling">
      <album-item v-for="item in displayedItems" :album="item"/>
      <span class="item filler" v-for="n in 6"/>
      <to-top-button/>
    </div>
  </section>
</template>

<script>
import { filterBy, limitBy, orderBy, event } from '../../../utils'
import { albumStore } from '../../../stores'
import albumItem from '../../shared/album-item.vue'
import viewModeSwitch from '../../shared/view-mode-switch.vue'
import sortModeSwitch from '../../shared/sort-mode-switch.vue'
import infiniteScroll from '../../../mixins/infinite-scroll'

export default {
  mixins: [infiniteScroll],
  components: { albumItem, viewModeSwitch, sortModeSwitch },

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
        this.datas = filterBy(albumStore.all, this.q, 'name', 'artist.name')
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

#albumsWrapper {
  .albums {
    @include artist-album-wrapper();
  }
}
</style>
