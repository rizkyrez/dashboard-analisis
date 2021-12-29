<template>
    <jb-buttons type="justify-start lg:justify-end" no-wrap>
        <jb-button color="warning" :icon="mdiMagnify" small @click="showDetail" />
        <jb-button color="success" :icon="mdiCircleEditOutline" small @click="editClass" />
        <jb-button color="danger" :icon="mdiTrashCan" small @click="deleteAction" />
    </jb-buttons>
</template>
<script>
import JbButtons from '@/components/JbButtons'
import JbButton from '@/components/JbButton'
import { mdiCircleEditOutline, mdiTrashCan, mdiMagnify } from '@mdi/js'
export default {
  name: 'ButtonActionRender',
  components: {
    JbButtons,
    JbButton
  },
  setup (props) {
    // const editClass = () => {
    //     const tes = props.params.node.rowIndex
    //     // showModal()
    //     // classObject.value = kelas
    //     // showEdit.value = true
    // }
        return {
            mdiCircleEditOutline,
            mdiTrashCan,
            mdiMagnify
        }
    },
    methods: {
        showDetail (event) {
            // the grid component can now be accessed via this.params.context.componentParent
            this.params.context.componentParent.detailData(event, this.params.data)
        },
        editClass (event) {
            // the grid component can now be accessed via this.params.context.componentParent
            this.params.context.componentParent.editData(event, this.params.data)
        },
        deleteAction () {
            this.params.context.componentParent.deleteModalShow(this.params.value)
        }
    }

}
</script>
