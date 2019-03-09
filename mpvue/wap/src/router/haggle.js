
const haggle_index = () => import("@/pages/haggle/index/index.vue")
const haggle_lists = () => import("@/pages/haggle/lists/index.vue")
const haggle_detail = () => import("@/pages/haggle/haggle_detail/index.vue")
const haggle_top = () => import("@/pages/haggle/haggle_top/index.vue")
const haggle_instruction = () => import("@/pages/haggle/instruction/index.vue")
const haggle_help = () => import("@/pages/haggle/haggle_help/index.vue")

export default [
    {
        path: '/haggle/index/:id',
        name: 'haggle_index',
        component: haggle_index
    },
    {
        path: '/haggle/lists',
        name: 'haggle_lists',
        component: haggle_lists
    },
    {
        path: '/haggle/detail/:haggleid',
        name: 'haggle_detail',
        component: haggle_detail
    },
    {
        path: '/haggle/top/:orderid/:haggleid',
        name: 'haggle_top',
        component: haggle_top
    },
    {
        path: '/haggle/instruction/:eventinfoid',
        name: 'haggle_instruction',
        component: haggle_instruction
    },
    {
        path: '/haggle/help/:inviteuid/:haggleid',
        name: 'haggle_help',
        component: haggle_help
    },
]