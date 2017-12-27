import Vue from 'vue'
import { $http } from '@/plugins/http.js'

const Mixin = {
  data() {
    return {
      CITY_OPTIONS: [],
      PICKER_OPTIONS: {
        disabledDate(time) {
          return time.getTime() > Date.now();
        },
        shortcuts: [{
          text: '今天',
          onClick(picker) {
            picker.$emit('pick', new Date());
          }
        }, {
          text: '昨天',
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() - 3600 * 1000 * 24);
            picker.$emit('pick', date);
          }
        }, {
          text: '一周前',
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() - 3600 * 1000 * 24 * 7);
            picker.$emit('pick', date);
          }
        }]
      }
    }
  },
  filters: {
    formatCellContent(val, suffix) {
      if (!val)
        return '--'
      else if (suffix)
        return val + suffix
      return val
    }
  },
  created() {
    $http.post('/Caryu/Common/getAdminCity')
      .then(res => this.CITY_OPTIONS = res)
  }
}

export default Mixin