import axios from 'axios';

import type HasFieldsetData from "../Interfaces/HasFieldsetData";

/**
 * Submission class.
 * 
 * @since 1.0.0
 */
export default class Request {    
    private url    : string;
    private method : string;
    private nonce  : string;
    private data   : HasFieldsetData[];

    /**
     * Submission data.
     * 
     * @param submissionData Submission data.
     * 
     * @since 1.0.0
     */
    public constructor( url: string, method: string, data: HasFieldsetData[], nonce: string ) {
        this.url    = url;
        this.method = method;
        this.data   = data;
        this.nonce  = nonce;
    }

    public send() : Promise {
        let method = this.method.toUpperCase();
        let result;

        switch( method ) {
            case 'POST':
                result = this.post();
                break;
            case 'GET':
                result = this.get();
                break;
            case 'PUT':
                result = this.put();
                break;
        }

        return result;
    };

    private post() {
        const options = {
            headers: {'X-WP-Nonce': this.nonce}
        };
          
        return axios.post( this.url, { data: this.data } , options );        
    }

    private get() {
        return axios({
            method: 'GET',
            headers: {
                'content-type': 'application/json',
                'X-WP-Nonce': this.nonce
            },
            url: this.url,
            data: {
                data: this.data       
            } 
        });
    }

    private put() {
        return axios({
            method: 'PUT',
            headers: {
                'content-type': 'application/json',
                'X-WP-Nonce': this.nonce
            },
            url: this.url,
            data: {
                nonce: this.nonce,
                data: this.data                
            }
        });
    }
}