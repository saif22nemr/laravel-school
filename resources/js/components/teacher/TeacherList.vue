<template>
    <div>
        {{test}}
        {{allTeachers}}
        test
        <ul v-if="teachers">
            <li>test</li>

            <li v-for="(teacher, index) in teachers" :key="index">
                <!-- {{index}} Teacher of {{teacher.title}} -->
                {{teacher.title}}

            </li>
        </ul>
        <button @click="consolelog">Console Log</button>
        {{consolelog()}}
    </div>
</template>

<script>

export default {
    props: {
        url: String,
        token: String,
    },
    data: function() {
        return {
            teachers : [],
            headers : {'Authorization' : 'Bearer '+this.token,'Accept' : 'application/json'},
            test: 'var'
        };
    },
    methods: {
        consolelog(){
            console.log('Console Logs');
            console.log(this.teachers);
        },
    },
    computed: {
        allTeachers: function(){

            axios({
                url: this.url+'/api/teacher',
                headers: this.headers,
                method: 'get'
            }).then(response => {
                console.log('success axios');
                console.log(response.data);
                this.teachers = response.data.data.data;

            }).catch(error => {
                console.log('error axios');
                console.log(error.message);
                this.teachers = error;
            });
            console.log('end');

        },
    }
};

</script>
