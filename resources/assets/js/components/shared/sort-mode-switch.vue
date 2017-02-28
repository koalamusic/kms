<template>
  <span class="sort-modes">
    <a class="name-asc" :class="{ active: mutatedSort === 'name-asc' }"
      title="Sort by name asc"
      @click.prevent="setSort('name-asc')"><i class="fa fa-sort-alpha-asc"></i></a>
    <a class="random" :class="{ active: mutatedSort === 'random' }"
      title="Sort in random order"
      @click.prevent="setSort('random')"><i class="fa fa-random"></i></a>
  </span>
</template>

<script>
import isMobile from 'ismobilejs'

import { event } from '../../utils'
import { preferenceStore as preferences } from '../../stores'

export default {
  props: ['sort', 'for'],

  data () {
    return {
      mutatedSort: this.sort
    }
  },

  computed: {
    /**
     * The preference key for local storage for persistent mode.
     *
     * @return {string}
     */
    preferenceKey () {
      return `${this.for}SortMode`
    }
  },

  methods: {
    setSort (sort) {
      preferences[this.preferenceKey] = this.mutatedSort = sort
      this.$parent.changeSortMode(sort)
    }
  },

  created () {
    event.on('koel:ready', () => {
      this.mutatedSort = preferences[this.preferenceKey]

      // If the value is empty, we set a default mode.
      if (!this.mutatedSort) {
        this.mutatedSort = 'name-asc'
      }

      this.setSort(this.mutatedSort)
    })
  }
}
</script>

<style lang="scss" scoped>
@import "../../../sass/partials/_vars.scss";
@import "../../../sass/partials/_mixins.scss";

.sort-modes {
  display: flex;
  flex: 0 0 64px;
  border: 1px solid rgba(255, 255, 255, .2);
  height: 2rem;
  border-radius: 5px;
  overflow: hidden;

  a {
    width: 50%;
    text-align: center;
    line-height: 2rem;
    font-size: 1rem;

    &.active {
      background: #fff;
      color: #111;
    }
  }

  @media only screen and(max-width: 768px) {
    flex: auto;
    width: 64px;
    margin-top: 8px;
  }
}
</style>
