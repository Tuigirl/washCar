export default [{
  name: 'CarWash',
  path: '/CarWash',
  component: () => import('@/pages/CarWash/Index'),
  meta: {
    title: '驾遇快洗'
  }
}, {
  name: 'CarWash__Valuation',
  path: '/CarWash__Valuation',
  component: () => import('@/pages/CarWash/Valuation'),
  meta: {
    title: '设备开启'
  }
}, {
  name: 'CarWash__FaultSubmit',
  path: '/CarWash__FaultSubmit',
  component: () => import('@/pages/CarWash/FaultSubmit'),
  meta: {
    title: '故障申报'
  }
}]