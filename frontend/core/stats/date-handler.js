class DateHandler {
    //Devuele el primer y ultimo dia de la semana
    getHours() { 
        let curr = new Date();
        let firstday = new Date(curr.setDate(curr.getDate() - curr.getDay() + 2));
        let lastday = new Date(curr.setDate(curr.getDate() - curr.getDay() + 3));
    
        const padLeft = n => ("00" + n).slice(-2);
    
        function formatDate(d) {
            let date = [padLeft(d.getDate()), padLeft(d.getMonth() + 1), d.getFullYear()].join("-");
            let format = date.split("-");
            let finalData = `${format[2]}-${format[1]}-${format[0]}`;
            return finalData;
        }
    
        return {
            initialHour: `${formatDate(firstday)} 00:00:00`,
            lastHour: `${formatDate(lastday)} 00:00:00`
        }
    }
}

export default DateHandler;