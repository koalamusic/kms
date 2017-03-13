/*eslint camelcase: ["error", {properties: "never"}]*/

import Vue from 'vue'
import { reduce, each, union, difference, take, filter, orderBy, assign } from 'lodash'
import { http } from '../services'
import { secondsToHis } from '../utils'
import { songStore, artistStore } from '.'

export const homeStore = {
  /**
   * Init the store.
   */
  init () {
    return new Promise((resolve, reject) => {
      http.get('home', ({ data }) => {
        resolve(data)
      }, error => reject(error))
    })
  }
}
