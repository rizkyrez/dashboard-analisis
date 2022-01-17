/* eslint-disable */
import {
  mdiAccountCircle,
  mdiAccountGroup,
  mdiAccountMultiple,
  mdiAccountTie,
  mdiSchool,
  mdiAccountBox,
  mdiCalendarClock,
  mdiAccountSupervisor,
  mdiClipboardAlert,
  mdiAlertCircle,
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
          to: '/calon-siswa/internal-vs-external',
          label: 'Internal vs External',
        },
        {
          to: '/calon-siswa/achievement',
          label: 'Achievement-LIVE',
        },
        {
          to: '/calon-siswa/csb-potensial-vs-achievement',
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
          to: '/siswa/class-occupancy',
          label: 'Class Occupancy',
        },
        {
          to: '/siswa/statistik-jumlah-kelas',
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
      label: 'Guru',
      subLabel: 'Guru',
      icon: mdiAccountTie,
      updateMark: true,
      menu: [
        {
          to: '/guru/success-report',
          label: 'Succes Report',
        },
        {
          to: '/guru/failure-report',
          label: 'Failure Report',
        },
        {
          to: '/guru/teacher-occupancy',
          label: 'Teachers Occupancy',
        },
        {
          to: '/guru/administrative',
          label: 'Administrativ Monitoring',
        },
      ]
    },
  ],
  'KEPEGAWAIAN',
  [
    {
        to: '/kepegawaian/agama',
        label: 'Agama',
        icon: mdiAccountBox
    },
    {
        to: '/kepegawaian/area',
        label: 'Area',
        icon: mdiViewList
    },
    {
        to: '/kepegawaian/pendidikan',
        label: 'Pendidikan',
        icon: mdiSchool
    },
    {
        to: '/kepegawaian/anak-pegawai',
        label: 'Anak Guru/Karyawan',
        icon: mdiAccountSupervisor
    },
    {
        to: '/kepegawaian/masa-kerja',
        label: 'Masa Kerja',
        icon: mdiCalendarClock
    },
    {
        to: '/kepegawaian/punishment',
        label: 'Punishment',
        icon: mdiClipboardAlert
    },

  ],
  'Keuangan',
  [
    {
        to: '/oustanding-ar',
        label: 'Oustanding AR',
        icon: mdiViewList
    },
  ]
]
