// 拼团
const collage_index = () => import("@/pages/collage/index/index.vue") 
const collage_lists = () => import("@/pages/collage/lists/index.vue") 
const collage_detail = () => import("@/pages/collage/collage_detail/index.vue")
const group_detail = () => import("@/pages/collage/group_detail/index.vue")
const collage_instruction = () => import("@/pages/collage/instruction/index.vue")
const group_more = () => import("@/pages/collage/group_more/index.vue")
const my_collage = () => import("@/pages/collage/my_collage/index.vue")



export default [
    {
        path: '/collage/index/:id',
        name: 'collage_index',
        component: collage_index
    },
    {
        path: '/collage/collage_detail/:id',
        name: 'collage_detail',
        component: collage_detail
    },
    {
        path: '/collage/group_detail/:id',
        name: 'group_detail',
        component: group_detail
    },
    {
        path: '/collage/group_more/:goodsid/:collageid',
        name: 'group_more',
        component: group_more
    },
    {
        path: '/collage/lists',
        name: 'collage_lists',
        component: collage_lists
    },
    {
        path: '/collage/instruction/:eventinfoid',
        name: 'collage_instruction',
        component: collage_instruction
    },
    {
        path: '/collage/my_collage/:id',
        name: 'my_collage',
        component: my_collage
    },
]