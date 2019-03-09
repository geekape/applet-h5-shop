<template>
  <div class="web-view">
    <web-view :src="url"></web-view>
  </div>
</template>

<script>

export default {
  data () {
    return {
			url: ''
    }
  },
  computed: {
   
  },

  components: {
    
  },

  methods: {

  },

  onLoad () {
    this.url = this.$root.$mp.query.url
  }
}
</script>


<style lang="scss" scoped>

</style>
