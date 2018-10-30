import React from "react";
import ReactDOM from "react-dom";

class Navigation extends React.Component {
  anchor(anchor) {
    const { i18n } = window.GSD;
    return <li key={anchor.NAME}>
      <a href={anchor.URL}>
        {i18n[anchor.NAME]}
      </a>
    </li>;
  }
  render() {
    const { i18n, menu } = window.GSD;

    return (
      <>
        <span className="menu">Menu</span>
        <ul>
        {menu.map(item => {
          if (item.PERMISSION) {
            const className = `fa ${item.ICON} fa-2x`;
           return <li key={item.NAME} className={item.ACTIVE && item.ACTIVE == item.URL ? "active" : ""}>
            <a href={item.URL} title="Layouts">
              <i className={className} />
              {i18n[item.NAME]}
            </a>
            {item.ITEMS && <ul>
              {item.ITEMS.map(this.anchor)}
            </ul>}
          </li>;
          }
        })}
        </ul>
      </>
    );
  }
}

ReactDOM.render(<Navigation active={window.activeSection} />, document.getElementById("navigation"));
