<template>
  <section id="albumsWrapper">
    <h1 class="heading">
      <span>Albums</span>
      <sort-mode-switch :sort="sorting" for="albums"/>
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
import { http } from '../../../services'

export default {
  mixins: [infiniteScroll],
  components: { albumItem, viewModeSwitch, sortModeSwitch },

  data () {
    return {
      perPage: 21,
      numOfItems: 21,
      q: '',
      viewMode: 'thumbnails',
      sorting: {
        key: 'name',
        reverse: false
      },
      datas: []
    }
  },

  computed: {
    displayedItems () {
      return limitBy(
        filterBy(this.datas, this.q, 'name', 'artist.name'),
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
      albumStore.init().then(function(albums) {
        self.datas = albums
        self.sortItems()
      }).catch(function() {
        console.log("Albums loading error")
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
