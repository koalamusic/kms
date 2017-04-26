<template>
  <section id="albumsWrapper">
    <h1 class="heading">
      <span>Albums</span>
      <sort-mode-switch :sort="sorting" for="albums"/>
      &nbsp;
      <view-mode-switch :mode="viewMode" for="albums"/>
    </h1>

    <div ref="scroller" class="albums main-scroll-wrap" :class="'as-'+viewMode" @scroll="scrolling">
      <album-item v-for="item in albums" :album="item"/>
      <span class="item filler" v-for="n in 7"/>
      <to-top-button/>

      <img v-if="!loaded" src="/img/bars.gif" alt="Sound bars" class="internal-loader" height="13" width="auto" />
    </div>
  </section>
</template>

<script>
import { filterBy, limitBy, showOverlay, hideOverlay, orderBy, event } from '../../../utils'
import { albumStore, searchStore } from '../../../stores'
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
      perPage: 100,
      numOfItems: 100,
      viewMode: 'thumbnails',
      sorting: {
        key: 'name',
        reverse: false
      }
    }
  },

  computed: {
      albums () {
          return limitBy(
              filterBy(albumStore.state.albums, searchStore.state.query, 'name', 'artist.name'),
              this.numOfItems
          )
      },
      loaded() {
          return albumStore.state.albums.length > 0
      }
  },

  created: function () {
      albumStore.dispatch('LOAD_ALBUMS')
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
        albumStore.state.albums = orderBy(albumStore.state.albums, this.sorting.key, this.sorting.reverse)
    }
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
