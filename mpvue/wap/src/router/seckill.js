
const seckill_detail = () => import('@/pages/seckill/detail/index.vue')
const seckill_index = () => import('@/pages/seckill/index/index.vue')
const seckill_lists = () => import('@/pages/seckill/lists/index.vue')

export default [
    {
        path: '/seckill/lists',
        name: 'seckill_lists',
        component: seckill_lists
      },
      {
        path: '/seckill/index/:id',
        name: 'seckill_index',
        component: seckill_index
      },
      {
        path: '/seckill/detail/:id',
        name: 'seckill_detail',
        component: seckill_detail
      }
]
