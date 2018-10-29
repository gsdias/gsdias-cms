import React from 'react';
import ReactDOM from 'react-dom';

class List extends React.Component {
    item(type, {ID, NAME, CREATED}) {
        const link = `/admin/${type}/${ID}/edit`;
        return <li key={ID}>
            <a href={link}>{NAME}</a> <em className="ago">({CREATED})</em>
        </li>
    }
    render() {
        const icon = `fa fa-${this.props.icon} fa-1x`;
        return (<><h1><i className={icon}></i> {this.props.title}</h1>
        <ul>
            {this.props.items.length ? this.props.items.map(this.item.bind(this, this.props.type)) : <li>{this.props.noitems}</li>}
        </ul></>)
    }
}

document.getElementById('pages') && ReactDOM.render(<List {...GSD.pages} />, document.getElementById('pages'));
document.getElementById('users') && ReactDOM.render(<List {...GSD.users} />, document.getElementById('users'));
document.getElementById('images') && ReactDOM.render(<List {...GSD.images} />, document.getElementById('images'));
document.getElementById('documents') && ReactDOM.render(<List {...GSD.documents} />, document.getElementById('documents'));