/*eslint camelcase: ["error", {properties: "never"}]*/

import Vue from 'vue'
import { reduce, each, union, difference, take, filter, orderBy, assign } from 'lodash'
import { http } from '../services'
import { secondsToHis } from '../utils'
import stub from '../stubs/album'
import { songStore, artistStore } from '.'

export const albumStore = {
  stub,
  cache: [],

  datas: {
    albums: [stub]
  },

  /**
   * Init the store.
   *
   * @param  {Array.<Object>} artists The array of artists to extract album data from.
   */
  init () {
    return new Promise((resolve, reject) => {
      http.get('albums', ({ data }) => {
        resolve(data.albums)
      }, error => reject(error))
    })
  },

  setupAlbum (album, artist) {
    Vue.set(album, 'playCount', 0)
    Vue.set(album, 'artist', artist)
    Vue.set(album, 'info', null)
    this.getLength(album)
    this.cache[album.id] = album

    return album
  },

  /**
   * Get all albums in the store.
   *
   * @return {Array.<Object>}
   */
  get all () {
    return this.datas.albums
  },

  /**
   * Set all albums.
   *
   * @param  {Array.<Object>} value
   */
  set all (value) {
    this.datas = value
  },

  byId (id) {
    return new Promise((resolve, reject) => {
      http.get('albums/' + id, ({ data }) => {
        resolve(data.album)
      }, error => reject(error))
    })
  },

  getSongs(album) {
    return new Promise((resolve, reject) => {
      http.get('albums/' + album.id + '/songs', ({ data }) => {
        resolve(data.songs)
      }, error => reject(error))
    })
  },

  /**
   * Get the total length of an album by summing up its songs' duration.
   * The length will also be converted into a H:i:s format and stored as fmtLength.
   *
   * @param  {Object} album
   *
   * @return {String} The H:i:s format of the album length.
   */
  getLength (album) {
    Vue.set(album, 'length', reduce(album.songs, (length, song) => length + song.length, 0))
    Vue.set(album, 'fmtLength', secondsToHis(album.length))

    return album.fmtLength
  },

  /**
   * Add new album/albums into the current collection.
   *
   * @param  {Array.<Object>|Object} albums
   */
  add (albums) {
    albums = [].concat(albums)
    each(albums, album => {
      this.setupAlbum(album, album.artist)
      album.playCount = reduce(album.songs, (count, song) => count + song.playCount, 0)
    })

    this.all = union(this.all, albums)
  },

  /**
   * Add song(s) into an album.
   *
   * @param {Object} album
   * @param {Array.<Object>|Object} song
   */
  addSongsIntoAlbum (album, songs) {
    songs = [].concat(songs)

    album.songs = union(album.songs || [], songs)

    each(songs, song => {
      song.albumId = album.id
      song.album = album
    })

    album.playCount = reduce(album.songs, (count, song) => count + song.playCount, 0)
    this.getLength(album)
  },

  /**
   * Remove song(s) from an album.
   *
   * @param  {Object} album
   * @param  {Array.<Object>|Object} songs
   */
  removeSongsFromAlbum (album, songs) {
    album.songs = difference(album.songs, [].concat(songs))
    album.playCount = reduce(album.songs, (count, song) => count + song.playCount, 0)
    this.getLength(album)
  },

  /**
   * Checks if an album is empty.
   *
   * @param  {Object}  album
   *
   * @return {boolean}
   */
  isAlbumEmpty (album) {
    return !album.songs.length
  },

  /**
   * Remove album(s) from the store.
   *
   * @param  {Array.<Object>|Object} albums
   */
  remove (albums) {
    albums = [].concat(albums)
    this.all = difference(this.all, albums)

    // Remove from the artist as well
    each(albums, album => {
      artistStore.removeAlbumsFromArtist(album.artist, album)

      // Delete the cache while we're here
      delete this.cache[album.id]
    })
  },

  /**
   * Get top n most-played albums.
   *
   * @param  {Number} n
   *
   * @return {Array.<Object>}
   */
  getMostPlayed (n = 6) {
    // Only non-unknown albums with actually play count are applicable.
    const applicable = filter(this.all, album => album.playCount && album.id !== 1)
    return take(orderBy(applicable, 'playCount', 'desc'), n)
  },

  /**
   * Get n most recently added albums.
   *
   * @param  {Number} n
   *
   * @return {Array.<Object>}
   */
  getRecentlyAdded (n = 6) {
    const applicable = filter(this.all, album => album.id !== 1)
    return take(orderBy(applicable, 'created_at', 'desc'), n)
  }
}
