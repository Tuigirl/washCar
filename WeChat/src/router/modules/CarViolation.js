export default [{
  name: 'CarViolation',
  path: '/CarViolation',
  component: () => import('@/pages/CarViolation/Index'),
  meta: {
    title: '违章查询'
  }
}, {
  name: 'CarViolation__List',
  path: '/CarViolation__List',
  component: () => import('@/pages/CarViolation/CarViolation__List'),
  meta: {
    title: '违章查询结果'
  }
}, {
  name: 'CarViolation__Detail',
  path: '/CarViolation__Detail',
  component: () => import('@/pages/CarViolation/CarViolation__Detail'),
  meta: {
    title: '违章详情'
  }
}, {
  name: 'CarViolation__ConfirmPay',
  path: '/CarViolation__ConfirmPay',
  component: () => import('@/pages/CarViolation/CarViolation__ConfirmPay'),
  meta: {
    title: '确认支付'
  }
}, {
  name: 'CarViolation__PaySuccess',
  path: '/CarViolation__PaySuccess',
  component: () => import('@/pages/CarViolation/CarViolation__PaySuccess'),
  meta: {
    title: '支付成功'
  }
}, {
  name: 'CarViolation__Orders',
  path: '/CarViolation__Orders',
  component: () => import('@/pages/CarViolation/CarViolation__Orders'),
  meta: {
    title: '违章订单'
  }
}]