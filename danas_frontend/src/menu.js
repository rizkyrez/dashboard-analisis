/* eslint-disable */
import {
  mdiAccountCircle,
  mdiDesktopMac,
  mdiGithub,
  mdiLock,
  mdiAlertCircle,
  mdiMonitorClean,
  mdiSquareEditOutline,
  mdiTable,
  mdiViewList,
  mdiTelevisionGuide,
  mdiResponsive
} from '@mdi/js'

export default [
  'Akademik',
  [
    {
      icon: mdiDesktopMac,
      label: 'Calon Siswa',
      subLabel: 'Calon Siswa',
      menu: [
        {
          to: '/tjcs',
          label: 'Trend Jumlah Calon Siswa',
        },
        {
          to: '/agama',
          label: 'Agama',
        },
        {
          to: '/area',
          label: 'Area',
        },
        {
          to: '/inex',
          label: 'Internal vs External',
        },
        {
          to: '/achievement',
          label: 'Achievement-LIVE',
        },
        {
          to: '/tarpon',
          label: 'Target Potensial CSB vs Achievement',
        },
      ]
    },
    {
      // to: '/tables',
      label: 'Siswa',
      icon: mdiTable,
      updateMark: true,
      menu: [
        {
          to: '/tjcs',
          label: 'Trend Jumlah Siswa',
        },
        {
          to: '/kelas',
          label: 'Class Occupancy',
        },
        {
          to: '/jumlahkelas',
          label: 'Jumlah Kelas per Tingkat',
        },
        {
          to: '/agama',
          label: 'Agama',
        },
        {
          to: '/area',
          label: 'Area',
        },
        {
          to: '/siblings',
          label: 'siblings',
        },
        // {
        //   to: '/potensisiswa',
        //   label: 'Strength Siswa',
        // },
        // {
        //   to: '/progress',
        //   label: 'Progress',
        // },
        // {
        //   to: '/progress',
        //   label: 'Rank',
        // },
      ]
    },
    {
      to: '/tables',
      label: 'Guru',
      subLabel: 'Guru',
      icon: mdiTable,
      updateMark: true,
      menu: [
        {
          to: '/report/success-report',
          label: 'Trend Jumlah Calon Siswa',
        },
        {
          to: '/agama',
          label: 'Agama',
        },
        {
          to: '/area',
          label: 'Area',
        },
        {
          to: '/inex',
          label: 'Internal vs External',
        },
        {
          to: '/achievement',
          label: 'Achievement-LIVE',
        },
        {
          to: '/tarpon',
          label: 'Target Potensial CSB vs Achievement',
        },
      ]
    },
  ],
  'Kepegawaian',
  [

    {
      label: 'Submenus',
      subLabel: 'Submenus Example',
      icon: mdiViewList,
      menu: [
        {
          label: 'Sub-item One'
        },
        {
          label: 'Sub-item Two'
        }
      ]
    }
  ],
  'Keuangan',
  [
    // {
    //   href: 'https://justboil.me/tailwind-admin-templates/vue-dashboard',
    //   label: 'Premium version',
    //   icon: mdiMonitorClean,
    //   target: '_blank'
    // },
    // {
    //   href: 'https://github.com/justboil/admin-one-vue-tailwind',
    //   label: 'GitHub',
    //   icon: mdiGithub,
    //   target: '_blank'
    // }
  ]
]
