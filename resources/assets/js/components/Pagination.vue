<template>
    <div>
        <ul class="pagination">
            <li class="page-item">
                <a class="page-link" :href="paginate.prev_page_url" aria-label="Previous">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">قبلی</span>
                </a>
            </li>
            <li class="page-item"><a class="page-link" :href="paginate.path+'?page='+paginate.current_page" v-text="paginate.current_page"></a></li>
            <li class="page-item">
                <a class="page-link" :href="paginate.next_page_url" aria-label="Next">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">بعدی</span>
                </a>
            </li>
        </ul>
    </div>
</template>


<script>
    export default{
        props:['paginate'],
        data(){
            return{

            }
        },
        methods:{
            paginateLink(){
                var mod = this.paginate.last_page % 5;

            }
        },
        mounted(){
            console.log(this.paginate)
        }
    }
</script>
