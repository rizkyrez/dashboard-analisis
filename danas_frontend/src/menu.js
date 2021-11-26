/* eslint-disable */
import {
  mdiAccountCircle,
  mdiAccountGroup,
  mdiAccountMultiple,
  mdiDesktopMac,
  mdiAlertCircle,
  mdiMonitorClean,
  mdiSquareEditOutline,
  mdiTable,
  mdiViewList,
} from '@mdi/js'

export default [
  'Akademik',
  [
    {
      icon: mdiAccountMultiple,
      label: 'Calon Siswa',
      subLabel: 'Calon Siswa',
      menu: [
        {
          to: '/calon-siswa/trends',
          label: 'Trend Jumlah Calon Siswa',
        },
        {
          to: '/calon-siswa/agama',
          label: 'Agama',
        },
        {
          to: '/calon-siswa/area',
          label: 'Area',
        },
        {
          to: '/calon-siswa/inex',
          label: 'Internal vs External',
        },
        {
          to: '/calon-siswa/achievement',
          label: 'Achievement-LIVE',
        },
        {
          to: '/calon-siswa/tarpon',
          label: 'Target Potensial CSB vs Achievement',
        },
      ]
    },
    {
      // to: '/tables',
      label: 'Siswa',
      subLabel: 'Siswa',
      icon: mdiAccountGroup,
      updateMark: true,
      menu: [
        {
          to: '/siswa/trends',
          label: 'Trend Jumlah Siswa',
        },
        {
          to: '/siswa/kelas',
          label: 'Class Occupancy',
        },
        {
          to: '/siswa/jumlahkelas',
          label: 'Jumlah Kelas per Tingkat',
        },
        {
          to: '/siswa/agama',
          label: 'Agama',
        },
        {
          to: '/siswa/area',
          label: 'Area',
        },
        {
          to: '/siswa/siblings',
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
          label: 'Succes Report',
        },
        {
          to: '/failure-report',
          label: 'Failure Report',
        },
        {
          to: '/teacher-occupancy',
          label: 'Teachers Occupancy',
        },
        {
          to: '/Administrative',
          label: 'Administrativ Monitoring',
        },
      ]
    },
  ],
  'KEPEGAWAIAN',
  [
    {
        to: '/agama',
        label: 'Agama',
        icon: mdiViewList
    },
    {
        to: '/area',
        label: 'Area',
        icon: mdiViewList
    },
    {
        to: '/pendidikan',
        label: 'Pendidikan',
        icon: mdiViewList
    },
    {
        to: '/masa-kerja',
        label: 'Masa Kerja',
        icon: mdiViewList
    },
    {
        to: '/punishment',
        label: 'Punishment',
        icon: mdiViewList
    },

  ],
  'Keuangan',
  [
    {
        to: '/oustanding-ar',
        label: 'Oustanding AR',
        icon: mdiViewList
    }
  ]
]
