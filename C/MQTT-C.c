//compile : gcc project2.c -o project2 -lmosquitto -lpthread -lmysqlclient -I/usr/include/json-c -ljson-c -lmysqlclient

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
//#include <time.h>

#include <mosquitto.h>
#define MQTT_SERVER     " YOU SERVER "
#define MQTT_PORT       1883
#define KEEP_ALIVE      60
#define MQTT_QOS_LEVEL  2
#define MQTT_SUB_TOPIC  "/sidewalksolve_data/#"
#define MSG_MAX_SIZE    1024

bool is_connected = false;
bool is_subscribed = false;
bool is_running = true;

#include <json.h>
#include <json_inttypes.h>
#include <json_object.h>
#include <json_tokener.h>
struct json_object *parsed_json;
struct json_object *from;
struct json_object *userID;
struct json_object *timestamp;
struct json_object *type;
struct json_object *text;

struct json_object *first_name;
struct json_object *last_name;
struct json_object *tel;
struct json_object *id_number;
struct json_object *address;
struct json_object *date;
struct json_object *time;
struct json_object *description;
struct json_object *photo_src;
struct json_object *video_src;

#include <mysql/mysql.h>
char query_buf[1024];
char time_buf[1024];
char query_string[1024];
char time_string[1024];

//////////////////////////////////////////////////////////////////
//MQTT - Mosquitto

void on_connect_cb( 
    struct mosquitto *mosq, void *userdata, int result );
    
void on_subscribe_cb(
    struct mosquitto *mosq, void *userdata, int mid, 
    int qos_count, const int *granted_qos );

void on_message_cb( 
    struct mosquitto *mosq, void *userdata, 
    const struct mosquitto_message *msg );

//////////////////////////////////////////////////////////////////

int main(int argc, char **argv)
{
    
    printf( "Project - SIDEWALKSOLVE...\n\n" );

    bool clean_session = true;
    struct mosquitto *mosq = NULL;
    

    printf( "MQTT : Mosquitto Client ...\n" );

    // Initialize the libmosquito routines
    mosquitto_lib_init();

    MYSQL *con = mysql_init(NULL);
    
    // Create a mosquitto client
    mosq = mosquitto_new( NULL, clean_session, NULL );
    if( !mosq )
    {
        printf( "MQTT : Create client failed..\n");
        mosquitto_lib_cleanup( );
        return 1;
    }
    
    // mosquitto_username_pw_set( mosq, "user","password" );
    
    // Set callback functions if necessary
    mosquitto_connect_callback_set( mosq, on_connect_cb );
    mosquitto_subscribe_callback_set( mosq, on_subscribe_cb );
    mosquitto_message_callback_set( mosq, on_message_cb );

    // Connect to server
    if (mosquitto_connect(mosq, MQTT_SERVER, MQTT_PORT, KEEP_ALIVE))
    {
        fprintf( stderr, "MQTT : Unable to connect.\n" );
        return 1;
    }

    if (con == NULL)
    {
        fprintf(stderr, "%s\n", mysql_error(con));
        exit(1);
    }
    
    int result;
    result = mosquitto_loop_start(mosq); 
    if (result != MOSQ_ERR_SUCCESS)
    {
        printf("MQTT : mosquitto loop error\n");
        return 1;
    }

    while (!is_connected) { usleep(100000); }
    
    mosquitto_subscribe( mosq, NULL, MQTT_SUB_TOPIC, MQTT_QOS_LEVEL );
    
    while (!is_subscribed) { usleep(100000); }
    
    while(is_running) { usleep(100000); }

    if (mosq) { mosquitto_destroy( mosq ); }

    mosquitto_lib_cleanup();

	return 0;
}



void on_connect_cb(struct mosquitto *mosq, void *userdata, int result )
{
    if (result!=0) { fprintf( stderr, "MQTT : Connect failed\n" ); }
    else
    {
	    is_connected = true;
        printf( "MQTT : Connect OK\n" );
    }
}
    
void on_subscribe_cb(
    struct mosquitto *mosq, void *userdata, int mid, 
    int qos_count, const int *granted_qos )
{
    is_subscribed = true;
	printf( "MQTT : Subscribe OK, (QOS=%d)\n", granted_qos[0] );
}

void on_message_cb( 
    struct mosquitto *mosq, void *userdata, 
    const struct mosquitto_message *msg )
{
	int len = msg->payloadlen;
    printf( "MQTT : Message received: %d byte(s)\n", len );
    if ( len > 0 )
    {
        printf( "MQTT : topic='%s'\nmsg=\n%s\n",msg->topic, (char *)msg->payload );

        parsed_json = json_tokener_parse(msg->payload);

        json_object_object_get_ex(parsed_json, "from", &from);   

        if(strcmp(json_object_get_string(from), "WWW")==0)
        {
            /**************************************************************
             *  __          ____          ____          __
             *  \ \        / /\ \        / /\ \        / /
             *   \ \  /\  / /  \ \  /\  / /  \ \  /\  / / 
             *    \ \/  \/ /    \ \/  \/ /    \ \/  \/ /  
             *     \  /\  /      \  /\  /      \  /\  /   
             *      \/  \/        \/  \/        \/  \/    
             * 
             * ************************************************************/
            
            /*
            json_object_object_get_ex(parsed_json, "userID", &userID);
            json_object_object_get_ex(parsed_json, "timestamp", &timestamp);
            json_object_object_get_ex(parsed_json, "type", &type);
            json_object_object_get_ex(parsed_json, "text", &text);

            json_object_object_get_ex(text, "first_name", &first_name);
            json_object_object_get_ex(text, "last_name", &last_name);
            json_object_object_get_ex(text, "tel", &tel);
            json_object_object_get_ex(text, "id_number", &id_number);
            json_object_object_get_ex(text, "address", &address);
            json_object_object_get_ex(text, "date", &date);
            json_object_object_get_ex(text, "time", &time);
            json_object_object_get_ex(text, "description", &description);
            */
            json_object_object_get_ex(parsed_json, "timestamp", &timestamp);
            json_object_object_get_ex(parsed_json, "first_name", &first_name);
            json_object_object_get_ex(parsed_json, "last_name", &last_name);
            json_object_object_get_ex(parsed_json, "tel", &tel);
            json_object_object_get_ex(parsed_json, "id_number", &id_number);
            json_object_object_get_ex(parsed_json, "address", &address);
            json_object_object_get_ex(parsed_json, "date", &date);
            json_object_object_get_ex(parsed_json, "description", &description);
            json_object_object_get_ex(parsed_json, "photo_src", &photo_src);
			json_object_object_get_ex(parsed_json, "video_src", &video_src);

            printf("\n============================================\n\n");

            printf("from: %s\n", json_object_get_string(from));
            printf("timestamp: %s\n", json_object_get_string(timestamp));
	        //printf("userID: %s\n", json_object_get_string(userID));
            printf("FirstName: %s\n", json_object_get_string(first_name));
	        printf("LastName: %s\n", json_object_get_string(last_name));
            printf("Tel: %s\n", json_object_get_string(tel));
            printf("ID_number: %s\n", json_object_get_string(id_number));
            printf("Address: %s\n", json_object_get_string(address));
            printf("Date: %s\n", json_object_get_string(date));
            //printf("Time: %s\n", json_object_get_string(time));
            printf("Description: %s\n", json_object_get_string(description));
            printf("photo_src: %s\n", json_object_get_string(photo_src));
			printf("video_src: %s\n", json_object_get_string(video_src));

            printf("\n============================================\n\n");
            /*
            sprintf(time_string,"%sT%s",
                    json_object_get_string(date),
                    json_object_get_string(time)
            );
            printf(time_string);
            */
            char query_string[1024] = 
            {
                "INSERT INTO sws_data( from_where, timestamp, firstname, lastname, idcard_num, tel_num, address, time, description,photo_src,video_src) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')"
            };
            sprintf(query_buf,query_string,
                    json_object_get_string(from),
                    json_object_get_string(timestamp),
                    json_object_get_string(first_name),
                    json_object_get_string(last_name),
                    json_object_get_string(id_number),
                    json_object_get_string(tel),
                    json_object_get_string(address),
                    json_object_get_string(date),
                    json_object_get_string(description),
                    json_object_get_string(photo_src),
					json_object_get_string(video_src)
                );
            printf(query_buf);

        }
        else if(strcmp(json_object_get_string(from), "LINE")==0)
        {
            /**************************************************************
             *   _      _____ _   _ ______ 
             *  | |    |_   _| \ | |  ____|
             *  | |      | | |  \| | |__   
             *  | |      | | | . ` |  __|  
             *  | |____ _| |_| |\  | |____ 
             *  |______|_____|_| \_|______|
             * 
             * ************************************************************/
            json_object_object_get_ex(parsed_json, "userID", &userID);
            json_object_object_get_ex(parsed_json, "timestamp", &timestamp);
            json_object_object_get_ex(parsed_json, "type", &type);
            json_object_object_get_ex(parsed_json, "text", &text);

            json_object_object_get_ex(text, "first_name", &first_name);
            json_object_object_get_ex(text, "last_name", &last_name);
            json_object_object_get_ex(text, "tel", &tel);
            json_object_object_get_ex(text, "id_number", &id_number);
            json_object_object_get_ex(text, "address", &address);
            json_object_object_get_ex(text, "date", &date);
            json_object_object_get_ex(text, "time", &time);
            json_object_object_get_ex(text, "description", &description);

            printf("\n============================================\n\n");

            printf("from: %s\n", json_object_get_string(from));
            printf("timestamp: %s\n", json_object_get_string(timestamp));
	        printf("userID: %s\n", json_object_get_string(userID));
            printf("FirstName: %s\n", json_object_get_string(first_name));
	        printf("LastName: %s\n", json_object_get_string(last_name));
            printf("Tel: %s\n", json_object_get_string(tel));
            printf("ID_number: %s\n", json_object_get_string(id_number));
            printf("Address: %s\n", json_object_get_string(address));
            printf("Date: %s\n", json_object_get_string(date));
            printf("Time: %s\n", json_object_get_string(time));
            printf("Description: %s\n", json_object_get_string(description));

            printf("\n============================================\n\n");

            sprintf(time_string,"%sT%s",
                    json_object_get_string(date),
                    json_object_get_string(time)
            );
            printf(time_string);

            char query_string[1024] = 
            {
                "INSERT INTO sws_data(timestamp,from_where, userID, firstname, lastname, idcard_num, tel_num, address, time, description) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')" 
            };
            sprintf(query_buf,query_string,
                    json_object_get_string(timestamp),
                    json_object_get_string(from),
                    json_object_get_string(userID),
                    json_object_get_string(first_name),
                    json_object_get_string(last_name),
                    json_object_get_string(id_number),
                    json_object_get_string(tel),
                    json_object_get_string(address),
                    time_string,
                    json_object_get_string(description)
                );
            printf(query_buf);

        }
        else if(strcmp(json_object_get_string(from), "FB")==0)
        {
            /**************************************************************
             *   ______ ____  
             *  |  ____|  _ \ 
             *  | |__  | |_) |
             *  |  __| |  _ < 
             *  | |    | |_) |
             *  |_|    |____/ 
             * 
             * ************************************************************/
            json_object_object_get_ex(parsed_json, "userID", &userID);
            json_object_object_get_ex(parsed_json, "timestamp", &timestamp);
            json_object_object_get_ex(parsed_json, "type", &type);
            json_object_object_get_ex(parsed_json, "text", &text);

            json_object_object_get_ex(text, "first_name", &first_name);
            json_object_object_get_ex(text, "last_name", &last_name);
            json_object_object_get_ex(text, "tel", &tel);
            json_object_object_get_ex(text, "id_number", &id_number);
            json_object_object_get_ex(text, "address", &address);
            json_object_object_get_ex(text, "date", &date);
            json_object_object_get_ex(text, "time", &time);
            json_object_object_get_ex(text, "description", &description);

            printf("\n============================================\n\n");

            printf("from: %s\n", json_object_get_string(from));
            printf("timestamp: %s\n", json_object_get_string(timestamp));
	        printf("userID: %s\n", json_object_get_string(userID));
            printf("FirstName: %s\n", json_object_get_string(first_name));
	        printf("LastName: %s\n", json_object_get_string(last_name));
            printf("Tel: %s\n", json_object_get_string(tel));
            printf("ID_number: %s\n", json_object_get_string(id_number));
            printf("Address: %s\n", json_object_get_string(address));
            printf("Date: %s\n", json_object_get_string(date));
            printf("Time: %s\n", json_object_get_string(time));
            printf("Description: %s\n", json_object_get_string(description));

            printf("\n============================================\n\n");

            sprintf(time_string,"%sT%s",
                    json_object_get_string(date),
                    json_object_get_string(time)
            );
            printf(time_string);

            char query_string[1024] = 
            {
                "INSERT INTO sws_data(timestamp,from_where, userID, firstname, lastname, idcard_num, tel_num, address, time, description) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')" 
            };
            sprintf(query_buf,query_string,
                    json_object_get_string(timestamp),
                    json_object_get_string(from),
                    json_object_get_string(userID),
                    json_object_get_string(first_name),
                    json_object_get_string(last_name),
                    json_object_get_string(id_number),
                    json_object_get_string(tel),
                    json_object_get_string(address),
                    time_string,
                    json_object_get_string(description)
                );
            printf(query_buf);
        }

        MYSQL *con = mysql_init(NULL);

        if (mysql_real_connect(con, "sidewalksolve.xyz", "u722950798_admin", "Rsp010123131", "u722950798_sidewalksolve", 0, NULL, 0) == NULL)
        {
            printf("\nMySQL : ERROR\n");
            finish_with_error(con);
        }

        if (mysql_query(con, query_buf))
        {
            printf("\nMySQL : ERROR\n");
            finish_with_error(con);
        }

        printf("\nMySQL : SUCCESS\n\n");
        
    }
    else
    { // the received message is empty
		is_running = false;
	}
    fflush( stdout ); // flush the standard output
}

void finish_with_error(MYSQL *con)
{
    fprintf(stderr, "%s\n", mysql_error(con));
    mysql_close(con);
    exit(1);
}


