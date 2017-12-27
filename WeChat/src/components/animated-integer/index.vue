<template>
  <span>{{ tweeningValue }}</span>
</template>

<script>
import { TWEEN } from '@/utils'

export default {
  naem: 'AnimatedInteger',

  props: {
    value: {
      type: Number,
      required: true
    }
  },

  data() {
    return {
      tweeningValue: 0
    }
  },

  watch: {
    value(newValue, oldValue) {
      this.tween(oldValue, newValue)
    }
  },
  mounted() {
    this.tween(0, this.value)
  },
  methods: {
    tween(startValue, endValue) {
      var vm = this
      function animate(time) {
        requestAnimationFrame(animate)
        TWEEN.update(time)
      }
      new TWEEN.Tween({ tweeningValue: startValue })
        .to({ tweeningValue: endValue }, 450)
        .onUpdate(function() {
          vm.tweeningValue = this.tweeningValue.toFixed(0)
        })
        .start()
      animate()
    }
  }
}
</script>
