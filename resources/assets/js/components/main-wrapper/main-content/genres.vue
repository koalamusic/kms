<template>
  <section id="genresWrapper">
    <h1 class="heading">
      <span>Genres</span>
      <view-mode-switch :mode="viewMode" for="genres"/>
    </h1>

    <div class="genres main-scroll-wrap" :class="'as-'+viewMode" @scroll="scrolling">
      <genre-item v-for="item in displayedItems" :genre="item"/>
      <span class="item filler" v-for="n in 6"/>
      <to-top-button/>
    </div>
  </section>
</template>

<script>
import { filterBy, limitBy, event } from '../../../utils'
import { genreStore } from '../../../stores'
import genreItem from '../../shared/genre-item.vue'
import viewModeSwitch from '../../shared/view-mode-switch.vue'
import infiniteScroll from '../../../mixins/infinite-scroll'

export default {
  mixins: [infiniteScroll],
  components: { genreItem, viewModeSwitch },

  data () {
    return {
      perPage: 100,
      numOfItems: 100,
      q: '',
      viewMode: null,
      datas: []
    }
  },

  computed: {
    displayedItems () {
      return limitBy(
        filterBy(this.datas, this.q, 'name', 'genre.name'),
        this.numOfItems
      )
    }
  },

  methods: {
    changeViewMode (mode) {
      this.viewMode = mode
    },
    init() {
      var self = this
      genreStore.init().then(function(genres) {
        self.datas = genres
      }).catch(function() {
        console.log("Genres loading error")
      })
    }
  },

  created () {
    this.init(),

    event.on({
      'koel:teardown': () => {
        this.q = ''
        this.numOfItems = 100
      },

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

#genresWrapper {
  .genres {
    @include genre-wrapper();
  }
}
</style>
